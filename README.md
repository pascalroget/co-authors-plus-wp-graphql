# co-authors-plus-wp-graphql
Adds Co-Authors Plus Support to WPGraphQL

## Notes
This will only show published authors. See https://www.wpgraphql.com/2020/12/11/allowing-wpgraphql-to-show-unpublished-authors-in-user-queries.

## Usage
Retrieving authors is done by requesting the `authors` field. Example:

```js
query NewQuery {
  nodeByUri(uri: "this-is-a-test-post/") {
    ... on Post {
      id
      title
      authors(first: 10) {
        nodes {
          databaseId
          name
        }
      }
    }
  }
}
```

Response:

```json
{
  "data": {
    "nodeByUri": {
      "id": "cG9zdDo1",
      "title": "This is a test post",
      "authors": {
        "nodes": [
          {
            "databaseId": 1,
            "name": "admin"
          },
          {
            "databaseId": 2,
            "name": "Koda The Dog"
          }
        ]
      }
    }
  }
 }
 ```
 
 ## Shoutouts
 
[David Levine @ AxePress Development](https://github.com/axewp)
