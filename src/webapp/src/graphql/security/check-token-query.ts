import gql from 'graphql-tag'

export const CHECK_VALID_TOKEN = gql`
  query checkValidToken($token: String!) {
    checkValidToken(token: $token) {
      id
    }
  }
`
