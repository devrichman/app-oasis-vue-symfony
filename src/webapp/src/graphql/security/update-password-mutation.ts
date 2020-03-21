import gql from 'graphql-tag'

export const UPDATE_PASSWORD = gql`
  mutation UpdatePassword($token: String!, $password: String!) {
    updatePassword(token: $token, password: $password) {
      email
    }
  }
`;
