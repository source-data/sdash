const hashRegex = /\?search=(.*#[^&]*)(&.*)?/

const queryStringDehasher = (routeObject) => {

  let hashQueryString = routeObject.fullPath.match(hashRegex)

  if (Array.isArray(hashQueryString)) return hashQueryString[1]

  return routeObject.query.search || false

}

export default queryStringDehasher