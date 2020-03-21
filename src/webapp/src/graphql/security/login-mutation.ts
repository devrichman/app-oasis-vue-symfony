import gql from 'graphql-tag'
import {ME_FRAGMENT} from './me-fragment';

export const LOGIN = gql`
  mutation Login($userName: String!, $password: String!) {
    login(userName: $userName, password: $password) {
      ... on SerializableUser {
        ...MeFragment
      }
    }
  }
  ${ME_FRAGMENT}
`
