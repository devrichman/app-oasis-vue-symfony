import gql from 'graphql-tag'

export const ACCEPT_CGU = gql`
  mutation AcceptCgu {
    acceptCgu {
      id
      email
      firstName
      lastName
      cguAccepted,
    }
  }
`;
