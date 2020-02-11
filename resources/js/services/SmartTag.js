const smartTagUrl = process.env.MIX_SMART_TAG_URL

const SmartTag = function(text, deduplicate = false) {

    let fetchTags = function(text){

        var fd = new FormData()

        fd.append("format", "xml")
        fd.append("text", text)

        var http = new XMLHttpRequest()
        http.timeout = 30000

        let deduplicateTags = function(tags) {

            if(tags.length < 1) return tags

            return _.uniqWith(tags, _.isEqual)

        }

        return new Promise(function(resolve, reject){

            // Setup our listener to process compeleted requests
            http.onreadystatechange = function () {

                // Only run if the http is complete
                if (http.readyState !== 4) return

                // Process the response
                if (http.status >= 200 && http.status < 300) {
                    // If successful
                    let tags = http.responseXML.getElementsByTagName("sd-tag")
                    var tagArray = []
                    for(let i=0; i<tags.length; i++){
                        let theTag = {}
                        theTag.name = tags[i].textContent

                        theTag.type = tags[i].attributes["type"] ? tags[i].attributes.getNamedItem("type").value : null
                        theTag.role = tags[i].attributes["role"] ? tags[i].attributes.getNamedItem("role").value : null
                        theTag.category = tags[i].attributes["category"] ? tags[i].attributes.getNamedItem("category").value : null

                        tagArray.push(theTag)
                    }

                    resolve(deduplicate ? deduplicateTags(tagArray) : tagArray)
                } else {
                    // If failed
                    reject({
                        status: http.status,
                        message: http.statusText
                    })
                }

            }


            try {
                http.open("POST", smartTagUrl)
                http.send(fd)
            } catch(e){
                reject({
                    status: "HTTP request failed",
                    message: e
                })
            }



        })

    }

    return fetchTags(text)

}

export default SmartTag