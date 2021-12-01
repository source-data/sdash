<template>
    <div class="container">
        <div class="row" style="margin: 50px auto;">
            <aside class="col-md-2">
                <nav id="toc"></nav>
            </aside>
            <div id="api-docs" class="col-md-10">
                <h3 class="pb-3 mb-4 border-bottom">
                    API Reference
                </h3>
                <p>
                    The SDash API is organized around REST. It has predictable
                    resource-oriented URLs, accepts form-encoded request bodies,
                    returns JSON-encoded responses, and uses standard HTTP
                    response codes, authentication, and verbs.
                </p>
                <p>
                    All API endpoints are relative to the base URL. For example,
                    the <code>/panels</code> endpoint refers to
                    <code>https://sdash.sourcedata.io/api/public/panels</code>.
                </p>
                <div class="card">
                    <h5 class="card-header">Base URL</h5>
                    <div class="card-body">
                        <code>https://sdash.sourcedata.io/api/public</code>
                    </div>
                </div>

                <!-- Panel List [START] -->
                <h4>Panel List</h4>
                <div class="card">
                    <h5 class="card-header">Endpoint</h5>
                    <div class="card-body">
                        <code>GET /panels</code>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Request</h5>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Position</th>
                                    <th>#</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>page</td>
                                    <td><code>integer</code></td>
                                    <td><code>body</code></td>
                                    <td><code>optional</code></td>
                                    <td>
                                        load a page of results (results are
                                        provided in pages of 20 records – i.e.
                                        page 2 = records 21-40)
                                    </td>
                                </tr>
                                <tr>
                                    <td>search</td>
                                    <td><code>string</code></td>
                                    <td><code>body</code></td>
                                    <td><code>optional</code></td>
                                    <td>
                                        search panel titles, tags, authors and
                                        captions for the given string
                                    </td>
                                </tr>
                                <tr>
                                    <td>tags</td>
                                    <td><code>integer[]</code></td>
                                    <td><code>body</code></td>
                                    <td><code>optional</code></td>
                                    <td>
                                        the tag identifiers of any of the
                                        existing tags in the database
                                    </td>
                                </tr>
                                <tr>
                                    <td>authors</td>
                                    <td>
                                        <code>integer[]</code>
                                        <code>string[]</code>
                                    </td>
                                    <td><code>body</code></td>
                                    <td><code>optional</code></td>
                                    <td>
                                        the user identifiers or ORCIDs of any of
                                        the registered authors in the database
                                    </td>
                                </tr>
                                <tr>
                                    <td>sortOrder</td>
                                    <td><code>string</code></td>
                                    <td><code>body</code></td>
                                    <td><code>optional</code></td>
                                    <td>
                                        defines the order of the records
                                        returned (one of: modification-date-asc,
                                        modification-date-desc,
                                        creation-date-asc, creation-date-desc,
                                        title-asc, title-desc)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Response</h5>
                    <div class="card-body">
                        <pre><code class="language-json">{
    "STATUS": "200",
    "MESSAGE": "A list of public panels.",
    "DATA": {
        "current_page": 1,
        "data": [
            {
                "id": 126,
                "title": "Structure of 2019-nCoV S in the prefusion conformation",
                "caption": "(A) Schematic of 2019-nCoV S primary structure colored by domain. Domains that were excluded from the ectodomain expression construct or could not be visualized in the final map are colored white. SS, signal sequence; S2′, S2′ protease cleavage site; FP, fusion peptide; HR1, heptad repeat 1; CH, central helix; CD, connector domain; HR2, heptad repeat 2; TM, transmembrane domain; CT, cytoplasmic tail. Arrows denote protease cleavage sites. (B) Side and top views of the prefusion structure of the 2019-nCoV S protein with a single RBD in the up conformation. The two RBD down protomers are shown as cryo-EM density in either white or gray and the RBD up protomer is shown in ribbons colored corresponding to the schematic in (A).",
                "user_id": 1,
                "type": "image",
                "subtype": "jpeg",
                "version": 1,
                "is_public": 1,
                "clicks": 0,
                "downloads": 0,
                "created_at": "2020-12-01T23:41:59.000000Z",
                "updated_at": "2021-02-24T10:18:22.000000Z",
                "deleted_at": null,
                "user": {
                    "id": 1,
                    "firstname": "Terrence",
                    "surname": "Particle",
                    "role": "superadmin",
                    "department_name": "Communications",
                    "institution_name": "EMBO - Excellence in Life Sciences",
                    "orcid": "0000-0003-4001-0001"
                },
                "groups": [
                    {
                        "id": 5,
                        "name": "Lorem ipsum",
                        "description": "Dolor sit amet",
                        "user_id": 1,
                        "url": null,
                        "is_public": 1,
                        "created_at": "2020-12-01T23:45:59.000000Z",
                        "updated_at": "2021-02-10T13:13:30.000000Z",
                        "pivot": {
                            "panel_id": 126,
                            "group_id": 5,
                            "created_at": "2020-12-01T23:45:59.000000Z",
                            "updated_at": "2020-12-01T23:45:59.000000Z"
                        }
                    }
                ],
                "tags": [
                    {
                        "id": 7,
                        "content": "COVID-19",
                        "meta": {
                            "panel_id": 126,
                            "tag_id": 7,
                            "created_at": "2021-02-24T07:20:39.000000Z",
                            "updated_at": "2021-02-24T07:20:39.000000Z"
                        }
                    }
                ],
                "authors": [
                    {
                        "id": 1,
                        "firstname": "Terrence",
                        "surname": "Particle",
                        "department_name": "Communications",
                        "institution_name": "EMBO - Excellence in Life Sciences",
                        "orcid": "0000-0003-4001-0001",
                        "author_role": {
                            "panel_id": 126,
                            "user_id": 1,
                            "role": "corresponding",
                            "order": 0
                        }
                    }
                ],
                "external_authors": [
                    {
                        "id": 1,
                        "firstname": "John",
                        "surname": "Smith",
                        "department_name": null,
                        "institution_name": null,
                        "orcid": null,
                        "author_role": {
                            "panel_id": 126,
                            "external_author_id": 1,
                            "role": "author",
                            "order": 1
                        }
                    }
                ]
            },
            {
                "id": 43,
                "title": "Laudantium molestias molestiae natus.",
                "caption": "Quia est assumenda numquam ad ut corrupti. Et nemo minus itaque velit ducimus. Sed ut voluptatum rerum est. Eum quibusdam voluptatem eos ea autem nisi. Ab sunt facilis iure et nostrum ducimus.",
                "user_id": 2,
                "type": "numquam",
                "subtype": "et",
                "version": 0,
                "is_public": 1,
                "clicks": 3,
                "downloads": 7,
                "created_at": "2020-12-01T23:36:34.000000Z",
                "updated_at": "2020-12-01T23:36:34.000000Z",
                "deleted_at": null,
                "user": {
                    "id": 2,
                    "firstname": "Joseph",
                    "surname": "Matrix",
                    "role": "admin",
                    "department_name": "SourceData",
                    "institution_name": "EMBO - Excellence in Life Sciences",
                    "orcid": "0000-0003-2219-5841"
                },
                "groups": [
                    
                ],
                "tags": [
                    
                ],
                "authors": [
                    {
                        "id": 2,
                        "firstname": "Joseph",
                        "surname": "Matrix",
                        "department_name": "SourceData",
                        "institution_name": "EMBO - Excellence in Life Sciences",
                        "orcid": "0000-0003-2219-5841",
                        "author_role": {
                            "panel_id": 43,
                            "user_id": 2,
                            "role": "corresponding",
                            "order": 1
                        }
                    }
                ],
                "external_authors": [
                    
                ]
            }
        ],
        "first_page_url": "http://sdash.laravel/api/public/panels?page=1",
        "from": 1,
        "last_page": 1,
        "last_page_url": "http://sdash.laravel/api/public/panels?page=1",
        "next_page_url": null,
        "path": "http://sdash.laravel/api/public/panels",
        "per_page": 20,
        "prev_page_url": null,
        "to": 2,
        "total": 2
    }
}</code></pre>
                    </div>
                </div>
                <!-- Panel List [END] -->

                <!-- Group Panel List [START] -->
                <h4>Group Panel List</h4>
                <div class="card">
                    <h5 class="card-header">Endpoint</h5>
                    <div class="card-body">
                        <code>GET /groups/{group ID}/panels</code>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Request</h5>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Position</th>
                                    <th>#</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>tags</td>
                                    <td><code>integer[]</code></td>
                                    <td><code>body</code></td>
                                    <td><code>optional</code></td>
                                    <td>
                                        the tag identifiers of any of the
                                        existing tags in the database
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Response</h5>
                    <div class="card-body">
                        <div class="alert alert-primary" role="alert">
                            The same response as that of a Panel List.
                        </div>
                    </div>
                </div>
                <!-- Group Panel List [END] -->

                <!-- Single Panel [START] -->
                <h4>Single Panel</h4>
                <div class="card">
                    <h5 class="card-header">Endpoint</h5>
                    <div class="card-body">
                        <code>GET /panels/{panel ID}</code>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Request</h5>
                    <div class="card-body">
                        <div class="alert alert-secondary" role="alert">
                            No parameters are accepted.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Response</h5>
                    <div class="card-body">
                        <pre><code class="language-json">{
    "STATUS": "200",
    "MESSAGE": "Detailed view of Public Panel.",
    "DATA": [
        {
            "id": 126,
            "title": "Structure of 2019-nCoV S in the prefusion conformation",
            "caption": "(A) Schematic of 2019-nCoV S primary structure colored by domain. Domains that were excluded from the ectodomain expression construct or could not be visualized in the final map are colored white. SS, signal sequence; S2′, S2′ protease cleavage site; FP, fusion peptide; HR1, heptad repeat 1; CH, central helix; CD, connector domain; HR2, heptad repeat 2; TM, transmembrane domain; CT, cytoplasmic tail. Arrows denote protease cleavage sites. (B) Side and top views of the prefusion structure of the 2019-nCoV S protein with a single RBD in the up conformation. The two RBD down protomers are shown as cryo-EM density in either white or gray and the RBD up protomer is shown in ribbons colored corresponding to the schematic in (A).",
            "user_id": 1,
            "type": "image",
            "subtype": "jpeg",
            "version": 1,
            "is_public": 1,
            "clicks": 0,
            "downloads": 0,
            "created_at": "2020-12-01T23:41:59.000000Z",
            "updated_at": "2021-02-24T10:18:22.000000Z",
            "deleted_at": null,
            "user": {
                "id": 1,
                "firstname": "Terrence",
                "surname": "Particle",
                "department_name": "Communications",
                "institution_name": "EMBO - Excellence in Life Sciences",
                "role": "superadmin"
            },
            "tags": [
                {
                    "id": 7,
                    "content": "COVID-19",
                    "meta": {
                        "panel_id": 126,
                        "tag_id": 7,
                        "created_at": "2021-02-24T07:20:39.000000Z",
                        "updated_at": "2021-02-24T07:20:39.000000Z",
                        "id": 38,
                        "origin": "smarttag",
                        "role": "",
                        "type": "",
                        "category": ""
                    }
                }
            ],
            "groups": [
                {
                    "id": 5,
                    "name": "Lorem ipsum",
                    "description": "Dolor sit amet",
                    "user_id": 1,
                    "url": null,
                    "is_public": 1,
                    "created_at": "2020-12-01T23:45:59.000000Z",
                    "updated_at": "2021-02-10T13:13:30.000000Z",
                    "pivot": {
                        "panel_id": 126,
                        "group_id": 5,
                        "created_at": "2020-12-01T23:45:59.000000Z",
                        "updated_at": "2020-12-01T23:45:59.000000Z"
                    }
                },
                {
                    "id": 1,
                    "name": "Accusantium distinctio minus aperiam.",
                    "description": "Eos assumenda quia quaerat officiis modi non ex.",
                    "user_id": 1,
                    "url": "https://heathcote.com/delectus-explicabo-commodi-ut-possimus.html",
                    "is_public": 0,
                    "created_at": "2020-12-01T23:36:35.000000Z",
                    "updated_at": "2020-12-01T23:36:35.000000Z",
                    "pivot": {
                        "panel_id": 126,
                        "group_id": 1,
                        "created_at": "2020-12-01T23:46:15.000000Z",
                        "updated_at": "2020-12-01T23:46:15.000000Z"
                    }
                }
            ],
            "authors": [
                {
                    "id": 1,
                    "firstname": "Terrence",
                    "surname": "Particle",
                    "department_name": "Communications",
                    "institution_name": "EMBO - Excellence in Life Sciences",
                    "author_role": {
                        "panel_id": 126,
                        "user_id": 1,
                        "role": "corresponding",
                        "order": 0
                    }
                }
            ],
            "external_authors": [
                {
                    "id": 1,
                    "firstname": "John",
                    "surname": "Smith",
                    "department_name": null,
                    "institution_name": null,
                    "author_role": {
                        "panel_id": 126,
                        "external_author_id": 1,
                        "role": "author",
                        "order": 1
                    }
                }
            ],
            "files": [
                {
                    "id": 1,
                    "panel_id": 126,
                    "type": "url",
                    "original_filename": null,
                    "file_category_id": 1,
                    "url": "https://science.sciencemag.org/content/367/6483/1260",
                    "description": "lorem ipsum",
                    "mime_type": null,
                    "file_size": null,
                    "version": 0,
                    "is_archived": 0,
                    "created_at": "2020-12-01T23:42:55.000000Z",
                    "updated_at": "2020-12-01T23:44:14.000000Z"
                }
            ]
        }
    ]
}</code></pre>
                    </div>
                </div>
                <!-- Single Panel [END] -->

                <!-- Panel File List [START] -->
                <h4>Panel File List</h4>
                <div class="card">
                    <h5 class="card-header">Endpoint</h5>
                    <div class="card-body">
                        <code>GET /panels/{panel ID}/files</code>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Request</h5>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Position</th>
                                    <th>#</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>categoryId</td>
                                    <td><code>integer</code></td>
                                    <td><code>body</code></td>
                                    <td><code>optional</code></td>
                                    <td>the identifier of a file category</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Response</h5>
                    <div class="card-body">
                        <pre><code class="language-json"> {
    "STATUS": "200",
    "MESSAGE": "A list of sources of a public panel.",
    "DATA": [
        {
            "id": 1,
            "description": "lorem ipsum",
            "url": "https://science.sciencemag.org/content/367/6483/1260",
            "category_id": 1
        },
        {
            "id": 2,
            "description": "dolor sit amet",
            "url": "http://sdash.laravel/files/2",
            "category_id": 7
        }
    ]
}</code></pre>
                    </div>
                </div>
                <!-- Panel File List [END] -->

                <!-- Panel Image [START] -->
                <h4>Panel Image</h4>
                <div class="card">
                    <h5 class="card-header">Endpoint</h5>
                    <div class="card-body">
                        <code>GET /panels/{panel ID}/image</code>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Request</h5>
                    <div class="card-body">
                        <div class="alert alert-secondary" role="alert">
                            No parameters are accepted.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Response</h5>
                    <div class="card-body">
                        <div class="alert alert-primary" role="alert">
                            Returns the original image.
                        </div>
                    </div>
                </div>
                <!-- Panel Image [END] -->

                <!-- Panel Thumbnail [START] -->
                <h4>Panel Thumbnail</h4>
                <div class="card">
                    <h5 class="card-header">Endpoint</h5>
                    <div class="card-body">
                        <code>GET /panels/{panel ID}/image/thumbnail</code>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Request</h5>
                    <div class="card-body">
                        <div class="alert alert-secondary" role="alert">
                            No parameters are accepted.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Response</h5>
                    <div class="card-body">
                        <div class="alert alert-primary" role="alert">
                            Returns a thumbnail of the original image (300px in
                            height).
                        </div>
                    </div>
                </div>
                <!-- Panel Thumbnail [END] -->

                <!-- User List [START] -->
                <h4>User List</h4>
                <div class="card">
                    <h5 class="card-header">Endpoint</h5>
                    <div class="card-body">
                        <code>GET /users</code>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Request</h5>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Position</th>
                                    <th>#</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>name</td>
                                    <td><code>string</code></td>
                                    <td><code>body</code></td>
                                    <td><code>required</code></td>
                                    <td>
                                        text that will be matched against names
                                        of users
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Response</h5>
                    <div class="card-body">
                        <pre><code class="language-json">{
    "STATUS": "200",
    "MESSAGE": "A list of users",
    "DATA": [
        {
            "id": 2,
            "firstname": "Joseph",
            "surname": "Matrix",
            "institution_name": "EMBO - Excellence in Life Sciences"
        },
        {
            "id": 1,
            "firstname": "Terrence",
            "surname": "Particle",
            "institution_name": "EMBO - Excellence in Life Sciences"
        }
    ]
}</code></pre>
                    </div>
                </div>
                <!-- User List [END] -->

                <!-- Tag List [START] -->
                <h4>Tag List</h4>
                <div class="card">
                    <h5 class="card-header">Endpoint</h5>
                    <div class="card-body">
                        <code>GET /tags</code>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Request</h5>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Type</th>
                                    <th>Position</th>
                                    <th>#</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>name</td>
                                    <td><code>string</code></td>
                                    <td><code>body</code></td>
                                    <td><code>required</code></td>
                                    <td>
                                        text that will be matched against tag
                                        labels
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Response</h5>
                    <div class="card-body">
                        <pre><code class="language-json">{
    "STATUS": "200",
    "MESSAGE": "A list of tags",
    "DATA": [
        {
            "id": 7,
            "content": "COVID-19"
        }
    ]
}</code></pre>
                    </div>
                </div>
                <!-- Tag List [END] -->

                <!-- File Category List [START] -->
                <h4>File Category List</h4>
                <div class="card">
                    <h5 class="card-header">Endpoint</h5>
                    <div class="card-body">
                        <code>GET /files/categories</code>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Request</h5>
                    <div class="card-body">
                        <div class="alert alert-secondary" role="alert">
                            No parameters are accepted.
                        </div>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-header">Response</h5>
                    <div class="card-body">
                        <pre><code class="language-json">{
    "STATUS": "200",
    "MESSAGE": "List of file categories",
    "DATA": [
        {
            "id": 1,
            "name": "Paper"
        },
        {
            "id": 2,
            "name": "Code"
        },
        {
            "id": 3,
            "name": "Data"
        },
        {
            "id": 4,
            "name": "Methods"
        },
        {
            "id": 5,
            "name": "Reagents"
        },
        {
            "id": 6,
            "name": "Repository"
        },
        {
            "id": 7,
            "name": "Other"
        }
    ]
}</code></pre>
                    </div>
                </div>
                <!-- File Category List [START] -->
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "Api",

    data() {
        return {};
    },

    created() {
        this.$loadScript("/vendor/toc.js").then(() => {
            Toc.init({
                $nav: jQuery("#toc"),
                $scope: jQuery("#api-docs"),
            });
            jQuery("body").scrollspy({
                target: "#toc"
            });
        });
        this.$loadScript("/vendor/prism.js").then(() => {
            Prism.highlightAll();
        });
    },
};
</script>

<style lang="scss" scoped>
@import 'resources/sass/_colors.scss';
@import 'resources/sass/_layout.scss';
@import '/vendor/toc.css';
@import '/vendor/prism.css';

#toc {
    position: -webkit-sticky;
    position: sticky;
    top: $navbar-height + 1.5rem;
    z-index: 1000;
}
@media (min-width: 768px) {
    #toc {
        top: $navbar-height-md + 1.5rem;
    }
}

code[class*="language-"],
pre[class*="language-"] {
    margin: 0;
    padding: 0;
    font-size: 0.85rem;
    background: transparent;
}

h4 {
    padding: 0.5rem 0;
    border-bottom: 1px solid #dee2e6;
}

h4,
.card {
    margin-bottom: 1.5rem;
}

.alert {
    margin-bottom: 0;
}

.card-header {
    color: $mostly-black-blue;
}
</style>
