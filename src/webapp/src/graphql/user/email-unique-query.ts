import gql from 'graphql-tag'

export const EMAIL_UNIQUE = gql`
   query emailUnique($email: String!, $userId: String){
    emailUnique(email: $email, userId: $userId) 
   }
`;
