import gql from 'graphql-tag'

export const ROLE_NAME_UNIQUE = gql`
   query roleNameUnique($name: String!, $roleId: String){
    roleNameUnique(name: $name, roleId: $roleId) 
   }
`;
