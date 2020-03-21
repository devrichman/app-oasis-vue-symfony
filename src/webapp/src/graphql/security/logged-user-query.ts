import gql from 'graphql-tag'
import {ME_FRAGMENT} from './me-fragment';

export const LOGGED_USER = gql`
  query me {
    me {
      ... on SerializableUser {
       ...MeFragment
      }
    }
  }
  ${ME_FRAGMENT}
`
