import gql from 'graphql-tag'

export const RESET_PASSWORD = gql`
  mutation ResetPassword($email: String!) {
    resetPassword(email: $email)
  }
`;
