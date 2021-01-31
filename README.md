# ProductImporter
October CMS Product Import from JSON Export (etsy)

# Installation
* Extract to Plugins Directory
* Run ```php artisan october:up```
* Make post call to ```/api/import ``` with data in below mentioned format with following form-data:
    * login (plain text, backend user's user_name/login)
    * password (plain text, backend user's password)
    * file_name (file, JSON file with below mentioned content)

# Example of JSON
```yaml
{
    "id":"709214231",
    "name":"Product Title",
    "description":"Product Description",
    "images":[
        {
            "1x":"https:\/\/i.etsystatic.com\/20123673\/r\/il\/5df8e8\/1889875840\/il_1140xN.1889875840_37s7.jpg",
            "2x":"https:\/\/i.etsystatic.com\/20123673\/r\/il\/5df8e8\/1889875840\/il_1588xN.1889875840_37s7.jpg"
        },
        {
            "1x":"https:\/\/i.etsystatic.com\/20123673\/r\/il\/c41454\/1904354304\/il_1140xN.1904354304_dbkf.jpg",
            "2x":"https:\/\/i.etsystatic.com\/20123673\/r\/il\/c41454\/1904354304\/il_1588xN.1904354304_dbkf.jpg"
        }
    ],
    "variations":[
        {
            "label":"Material, Shape & Size",
            "options":[
                {
                    "id":"1166082849",
                    "value":" Brass Disc - 1\""
                },
                {
                    "id":"1148134676",
                    "value":" Brass Disc - 1 1\/4\""
                },
                {
                    "id":"1148134710",
                    "value":" Nickle Disc - 1\""
                },
                {
                    "id":"1148134728",
                    "value":" Nickle Disc - 1 1\/4\""
                },
                {
                    "id":"1166082895",
                    "value":" Copper Disc - 1\""
                },
                {
                    "id":"1149419972",
                    "value":" Copper Disc - 1 1\/4\""
                },
                {
                    "id":"1166082881",
                    "value":" Brass Hex - 1\""
                },
                {
                    "id":"1166082887",
                    "value":" Brass Hex - 1 1\/4\""
                },
                {
                    "id":"1647737236",
                    "value":" Nickle Hex - 1\""
                },
                {
                    "id":"1149419974",
                    "value":" Nickle Hex - 1 1\/4\""
                }
            ]
        },
        {
            "label":"Engraving of Contact Info",
            "options":[
                {
                    "id":"1166082855",
                    "value":" Contact info - No"
                },
                {
                    "id":"1166082871",
                    "value":" Contact info - Yes"
                }
            ]
        }
    ],
    "customisations":[
        "Add your personalization"
    ],
    "prices":[
        {
            "combo":[
                "1166082849",
                "1166082855"
            ],
            "price":"$28.63"
        },
        {
            "combo":[
                "1166082849",
                "1166082871"
            ],
            "price":"$35.79"
        },
        {
            "combo":[
                "1148134676",
                "1166082855"
            ],
            "price":"$31.50"
        },
        {
            "combo":[
                "1148134676",
                "1166082871"
            ],
            "price":"$38.65"
        },
        {
            "combo":[
                "1148134710",
                "1166082855"
            ],
            "price":"$28.63"
        },
        {
            "combo":[
                "1148134710",
                "1166082871"
            ],
            "price":"$35.79"
        },
        {
            "combo":[
                "1148134728",
                "1166082855"
            ],
            "price":"$31.50"
        },
        {
            "combo":[
                "1148134728",
                "1166082871"
            ],
            "price":"$38.65"
        },
        {
            "combo":[
                "1166082895",
                "1166082855"
            ],
            "price":"$28.63"
        },
        {
            "combo":[
                "1166082895",
                "1166082871"
            ],
            "price":"$35.79"
        },
        {
            "combo":[
                "1149419972",
                "1166082855"
            ],
            "price":"$31.50"
        },
        {
            "combo":[
                "1149419972",
                "1166082871"
            ],
            "price":"$38.65"
        },
        {
            "combo":[
                "1166082881",
                "1166082855"
            ],
            "price":"$28.63"
        },
        {
            "combo":[
                "1166082881",
                "1166082871"
            ],
            "price":"$35.79"
        },
        {
            "combo":[
                "1166082887",
                "1166082855"
            ],
            "price":"$31.50"
        },
        {
            "combo":[
                "1166082887",
                "1166082871"
            ],
            "price":"$38.65"
        },
        {
            "combo":[
                "1647737236",
                "1166082855"
            ],
            "price":"$28.63"
        },
        {
            "combo":[
                "1647737236",
                "1166082871"
            ],
            "price":"$35.79"
        },
        {
            "combo":[
                "1149419974",
                "1166082855"
            ],
            "price":"$31.50"
        },
        {
            "combo":[
                "1149419974",
                "1166082871"
            ],
            "price":"$38.65"
        }
    ],
    "reviews":[
        {
            "review_id":"2219002782",
            "reviewer":"erinisntcreativeatal",
            "reviewer_name":"erinisntcreativeatal",
            "review_rating":5,
            "review_date":"2021-01-29",
            "review_text":"Beautifully made and sturdy..worth the wait to be shipped internationally.",
            "reviewed_product":"709214231",
            "product_photo":"https:\/\/i.etsystatic.com\/1889875840\/d\/il\/5df8e8\/1889875840\/il_170x135.1889875840_37s7.jpg?version=0",
            "user_photo":"https:\/\/www.etsy.com\/images\/avatars\/default_avatar_75x75.png",
            "uploaded_photo":null
        }
    ],
    "stock_info":{
        "code":"available",
        "amount":"unknown"
    }
}
```
