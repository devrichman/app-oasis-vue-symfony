import {InMemoryCache} from "apollo-cache-inmemory";

/**
 *  This function deletes cached queries and is called after an Apollo Mutation
 *  in the update function (see userForm.vue).
 *
 *  Apollo is unreliable at refetching queries which do not have an id associated
 *  with them. It is also unreliable at refetching queries which can have
 *  multiple variables (like lists with sort and filters). In either case, it can cause
 *  the user to see old data. For example, they might not see the new user which they just
 *  created in users list because Apollo did not refetch their data.
 *
 *  In this case it is generally safer to delete the items from cache after a mutation.
 *  Alternative would be to use fetchPolicy: 'cache-with-network' but it would still
 *  show old data for a few seconds after a user is redirected to the list after creating.
 *  fetchPolicy: 'network-only' would solve that but it removes caching entirely which
 *  is not ideal either.
*/
export default function deleteQueriesFromApolloCache(cache: InMemoryCache, query: string) {
    let regex = new RegExp(query);
    Object.keys(cache['data'].data).forEach(key => {
        if (key.match(regex)) {
            cache['data'].delete(key)
        }
    });
}