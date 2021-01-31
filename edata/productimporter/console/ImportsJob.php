<?php
namespace Edata\ProductImporter\Console;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Offer;
use Lovata\PropertiesShopaholic\Models\Property;
use Lovata\PropertiesShopaholic\Models\PropertyValue;
use Lovata\PropertiesShopaholic\Models\PropertySet;
use Lovata\PropertiesShopaholic\Models\PropertyOfferLink;
use Lovata\PropertiesShopaholic\Models\PropertyValueLink;
class ImportsJob extends Command {
    protected $name = 'edata:importsjob';
    public function fire($job, $data) {
        if (!is_array($data)) {
            $job->delete();
            return true;
        }
        $file_id   = $data['id'];
        $file_name = storage_path('app/edata/') . $data['file_name'];
        if (!self::isJson(file_get_contents($file_name))) {
            echo "Not a JSON File: " . $data['org_file_name'] . "\r\n";
            DB::TABLE('edata_productimporter_files')->WHERE('id', $data['id'])->UPDATE(array(
                "status" => "Failed"
            ));
            $job->delete();
            return true;
        }
        $contents = json_decode(file_get_contents($file_name), true);
        if (!isset($contents['id'])) {
            echo "Can't Read File: " . $data['org_file_name'] . "\r\n";
            DB::TABLE('edata_productimporter_files')->WHERE('id', $data['id'])->UPDATE(array(
                "status" => "Failed"
            ));
            $job->delete();
            return true;
        }
        $category    = Category::first();
        $category_id = "";
        if (isset($category->id) && $category->id != "") {
            $category_id = $category->id;
        }
        $old_product   = false;
        $productExists = DB::TABLE('lovata_shopaholic_products')->WHERE('external_id', $contents['id'])->first();
        if (isset($productExists->id) && $productExists->id != "") {
            $product_id  = $productExists->id;
            $old_product = true;
            /*$updateData = array(
                "external_id" => $contents['id'],
                "name" => $contents['name'],
                "category_id" => $category_id,
                "active" => "1",
                "slug" => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $contents['name']))),
                "description" => $contents['description'],
                "updated_at" => date("Y-m-d H:i:s")
            );
            DB::TABLE('lovata_shopaholic_products')->WHERE('id', $product_id)->UPDATE($updateData);*/
        } else {
            $insertData = array(
                "external_id" => $contents['id'],
                "name" => $contents['name'],
                "category_id" => $category_id,
                "active" => "1",
                "slug" => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $contents['name']))),
                "description" => $contents['description'],
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            );
            $product_id = DB::TABLE('lovata_shopaholic_products')->INSERTGETID($insertData);
        }
        if (isset($contents['images']) && is_array($contents['images']) && count($contents['images']) > 0 && isset($product_id) && $product_id != "" && !$old_product) {
            foreach ($contents['images'] as $key => $images) {
                if (is_array($images) && isset($images['2x'])) {
                    $link = $images['1x'];
                    if ($key == 0) {
                        $file = new \System\Models\File;
                        $file->fromUrl($link);
                        $file->attachment_id   = $product_id;
                        $file->attachment_type = "Lovata\Shopaholic\Models\Product";
                        $file->is_public       = true;
                        $images                = $file->replicate();
                        $pImages               = $file->replicate();
                        $file->field           = "logo";
                        $pImages->field        = "preview_image";
                        $images->field         = "images";
                        $file->save();
                        $images->save();
                        $pImages->save();
                    } else {
                        $file = new \System\Models\File;
                        $file->fromUrl($link);
                        $file->attachment_id   = $product_id;
                        $file->attachment_type = "Lovata\Shopaholic\Models\Product";
                        $file->is_public       = true;
                        $file->field           = "images";
                        $file->save();
                    }
                }
            }
        }
        if (isset($contents['reviews']) && is_array($contents['reviews']) && count($contents['reviews']) > 0 && isset($product_id) && $product_id != "") {
            foreach ($contents['reviews'] as $review) {
                $reviewExists = DB::TABLE('lovata_reviews_shopaholic_reviews')->WHERE('external_id', $review['review_id'])->first();
                if (!isset($reviewExists->id)) {
                    $imgHtml = "";
                    if (isset($review['uploaded_photo']) && !empty($review['uploaded_photo']) && $review['uploaded_photo'] != "") {
                        $file = new \System\Models\File;
                        $file->fromUrl($review['uploaded_photo']);
                        $file->is_public = true;
                        $file->save();
                        $imgHtml = "<br/><img src = '" . $file->getPath() . "' />";
                    }
                    $insertData = array(
                        "external_id" => $review['review_id'],
                        "name" => $review['reviewer_name'],
                        "active" => "1",
                        "comment" => $review['review_text'] . $imgHtml,
                        "rating" => $review['review_rating'],
                        "product_id" => $product_id,
                        "created_at" => $review['review_date'] . " 00:00:00",
                        "updated_at" => $review['review_date'] . " 00:00:00"
                    );
                    $review_id  = DB::TABLE('lovata_reviews_shopaholic_reviews')->INSERTGETID($insertData);
                }
            }
        }
        if (isset($contents['variations']) && is_array($contents['variations']) && count($contents['variations']) > 0 && isset($contents['prices']) && is_array($contents['prices']) && count($contents['prices']) > 0 && isset($product_id) && $product_id != "") {
            $props         = array();
            $offers        = array();
            $prices        = array();
            $propsId       = array();
            $propSet       = "Offer Variations";
            $propSetExists = PropertySet::WHERE('name', $propSet)->first();
            if (isset($propSetExists->id) && $propSetExists->id != "") {
                $PropertySet = $propSetExists;
                $propSet_id  = $propSetExists->id;
            } else {
                $PropertySet       = new PropertySet;
                $PropertySet->name = $propSet;
                $PropertySet->code = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $propSet)));
                $PropertySet->save();
                $propSet_id = $PropertySet->id;
            }
            $categories = Category::PLUCK('id')->toArray();
            if (count($categories) > 0) {
                $PropertySet->category()->sync($categories, false);
            }
            $offers     = array();
            $optionsMap = array();
            $labelsMap  = array();
            foreach ($contents['variations'] as $variations) {
                $prop       = $variations['label'];
                $propExists = Property::WHERE('name', $prop)->first();
                if (isset($propExists->id) && $propExists->id != "") {
                    $Property         = $propExists;
                    $prop_id          = $propExists->id;
                    $labelsMap[$prop] = $prop_id;
                } else {
                    $Property           = new Property;
                    $Property->name     = $prop;
                    $Property->active   = "1";
                    $Property->code     = $Property->setSluggedValue('slug', 'name');
                    $Property->type     = "select";
                    $Property->settings = array(
                        "is_translatable" => "0",
                        "tab" => "Variations",
                        "datepicker" => "date",
                        "mediafinder" => "file"
                    );
                    $Property->save();
                    $prop_id          = $Property->id;
                    $labelsMap[$prop] = $prop_id;
                }
                if (isset($prop_id) && !empty($prop_id)) {
                    $props[]    = $prop_id;
                    $propValues = array();
                    foreach ($variations['options'] as $options) {
                        $propVal       = $options['value'];
                        $propValExists = PropertyValue::WHERE('slug', PropertyValue::getSlugValue($propVal))->first();
                        if (isset($propValExists->id) && $propValExists->id != "") {
                            $propValues[]                  = $propValExists->id;
                            $optionsMap[$options['value']] = $propValExists->id;
                        } else {
                            $PropertyValue        = new PropertyValue;
                            $PropertyValue->value = $propVal;
                            $PropertyValue->label = $propVal;
                            $PropertyValue->save();
                            $propValues[]                  = $PropertyValue->id;
                            $optionsMap[$options['value']] = $PropertyValue->id;
                        }
                    }
                    if (count($propValues) > 0) {
                        $Property->property_value()->sync($propValues, false);
                    }
                    $offerLinkExists = DB::TABLE('lovata_properties_shopaholic_set_offer_link')->WHERE('property_id', $prop_id)->WHERE('set_id', $propSet_id)->first();
                    if (!isset($offerLinkExists->property_id) || $offerLinkExists->property_id == "") {
                        $insertData = array(
                            "property_id" => $prop_id,
                            "set_id" => $propSet_id,
                            "in_filter" => "1",
                            "groups" => "0",
                            "filter_type" => "switch"
                        );
                        $product_id = DB::TABLE('lovata_properties_shopaholic_set_offer_link')->INSERTGETID($insertData);
                    }
                }
                foreach ($variations['options'] as $options) {
                    $offers[$options['id']]["id"]         = $options['id'];
                    $offers[$options['id']]["label"]      = $variations['label'];
                    $offers[$options['id']]["value"]      = $options['value'];
                    $offers[$options['id']]["prop_id"]    = $labelsMap[$variations['label']];
                    $offers[$options['id']]["propVal_id"] = $optionsMap[$options['value']];
                }
            }
            $prices   = array();
            $offerIds = array();
            foreach ($contents['prices'] as $price) {
                $temp = array();
                foreach ($price['combo'] as $offer_id) {
                    $temp[] = $offers[$offer_id];
                }
                $temp[]   = $price['price'];
                $prices[] = $temp;
            }
            foreach ($prices as $price_row) {
                $price       = 0;
                $offer_title = "";
                $prop        = array();
                foreach ($price_row as $data) {
                    if (!is_array($data)) {
                        $price = $data;
                    } else {
                        $offer_title .= $data['value'] . " + ";
                        $prop[$data['prop_id']] = $data['propVal_id'];
                    }
                }
                $offer_title = trim(rtrim($offer_title, " + "));
                $price       = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $offerExists = Offer::withTrashed()->WHERE('name', $offer_title)->WHERE('product_id', $product_id)->first();
                if (isset($offerExists->id) && $offerExists->id != "") {
                    if ($offerExists->trashed()) {
                        $offerExists->restore();
                    }
                    $offer_id   = $offerExists->id;
                    $offerIds[] = $offer_id;
                } else {
                    $offer             = new Offer;
                    $offer->active     = 1;
                    $offer->name       = $offer_title;
                    $offer->product_id = $product_id;
                    $offer->price      = $price;
                    $offer->save();
                    $offer_id   = $offer->id;
                    $offerIds[] = $offer_id;
                }
                foreach ($prop as $property_id => $value_id) {
                    $valueLinkExists = PropertyValueLink::WHERE('value_id', $value_id)->WHERE('property_id', $property_id)->WHERE('element_id', $offer_id)->WHERE('product_id', $product_id)->WHERE('element_type', DB::RAW('"Lovata\\\Shopaholic\\\Models\\\Offer"'))->first();
                    if (!isset($valueLinkExists->id) || $valueLinkExists->id == "") {
                        $PropertyValueLink               = new PropertyValueLink;
                        $PropertyValueLink->value_id     = $value_id;
                        $PropertyValueLink->property_id  = $property_id;
                        $PropertyValueLink->element_id   = $offer_id;
                        $PropertyValueLink->element_type = "Lovata\Shopaholic\Models\Offer";
                        $PropertyValueLink->product_id   = $product_id;
                        $PropertyValueLink->save();
                    }
                }
            }
            Offer::whereNotIn('id', $offerIds)->WHERE('product_id', $product_id)->delete();
        }
        if (isset($product_id) && $product_id != "") {
            DB::TABLE('edata_productimporter_files')->WHERE('id', $file_id)->UPDATE(array(
                "status" => "Imported"
            ));
            echo "Product Saved: " . $product_id . "\r\n";
        }
        $job->delete();
    }
    public function isJson($str) {
        $json = json_decode($str);
        return $json && $str != $json;
    }
}