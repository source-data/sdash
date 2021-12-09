#!/bin/bash
#
# gh-dl-release! It works!
# 
# This script downloads an asset from latest or specific Github release of a
# public repo. Feel free to extract more of the variables into command line
# parameters.
#
# PREREQUISITES
#
# curl, wget, jq
usage() {
  cat << EOF
Usage: $0 <repo identifier> <tag name> <asset name>

Download an asset from a public Github repository.

repo identifier:    Identifier of the Github repository from where to download the asset, in the form of <author>/<repository name>. Example: source-data/sdash
tag name:           The version of the asset to download. Enter "latest" to fetch the most recent tag.
asset name:         The name of the asset to download. 

Example:

    gh-dl-release.sh source-data/sdash latest public.tgz
EOF
}
REPO_ID=$1
VERSION=$2
ASSET_NAME=$3

if [ -z "${REPO_ID}" ] || [ -z "${VERSION}" ] || [ -z "${ASSET_NAME}" ]; then
    usage && exit 1
fi

if [ "${VERSION}" != "latest" ]; then
    VERSION="tags/${VERSION}"
fi

REPO_URL="https://api.github.com/repos/${REPO_ID}"
ASSET_DATA=`curl -H "Accept: application/vnd.github.v3+json" -s $REPO_URL/releases/$VERSION`
ASSET_ID=`echo ${ASSET_DATA} | jq ".assets | map(select(.name == \"${ASSET_NAME}\")) | first.id"`
echo "ASSET_ID: ${ASSET_ID}"

if [ "$ASSET_ID" = "null" ]; then
  >&2 echo "ERROR: version \"$VERSION\" not found"
  exit 1
fi;

curl --location -OJ -H "Accept:application/octet-stream" $REPO_URL/releases/assets/$ASSET_ID