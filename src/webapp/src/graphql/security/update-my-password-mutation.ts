import gql from 'graphql-tag'

export const UPDATE_MY_PASSWORD = gql`
  mutation UpdateMyPassword($password: String!) {
    updateMyPassword(password: $password) {
      email
    }
  }
`;
