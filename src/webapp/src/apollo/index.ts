import Vue from 'vue'
import VueApollo from 'vue-apollo'
import ApolloClient from 'apollo-client'
import { createUploadLink } from 'apollo-upload-client'
import { ServerError, ServerParseError } from 'apollo-link-http-common'
import { onError } from 'apollo-link-error'
import { InMemoryCache } from 'apollo-cache-inmemory'
import {typeDefs} from "@/apollo/typeDefs";
import {initLocalCache} from "@/apollo/initLocalCache";

Vue.use(VueApollo)

const uploadLink = createUploadLink({
  uri: process.env.VUE_APP_GRAPHQL_HTTP,
  credentials: 'include',
})

function isServerError(
    networkError: Error | ServerError | ServerParseError
): networkError is ServerError {
  return (<ServerError>networkError).statusCode !== undefined
}

function isServerParseError(
    networkError: Error | ServerError | ServerParseError
): networkError is ServerParseError {
  return (<ServerParseError>networkError).statusCode !== undefined
}

const onErrorLink = onError(({ graphQLErrors, networkError }) => {
  if (
      networkError &&
      (isServerError(networkError) || isServerParseError(networkError))
  ) {
    if (networkError.statusCode === 500) {
      //TODO router push internal error route name
    } else if (
        networkError.statusCode === 401
    ) {
      // got a 401 (not on login) so we logout the user.
      //TODO router push
    }
  }
})

const cache = initLocalCache(new InMemoryCache());

const apolloClient = new ApolloClient({
  link: onErrorLink.concat(uploadLink),
  typeDefs,
  resolvers: {},
  cache
})

const apolloProvider = createProvider(apolloClient)

export { apolloClient, apolloProvider }

function createProvider(apolloClient:any) {
  // Create vue apollo provider
  return new VueApollo({
    defaultClient: apolloClient,
    defaultOptions: {
      $query: {
      }
    },
    errorHandler(error) {
    }
  })
}
