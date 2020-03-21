import gql from 'graphql-tag';

export const typeDefs = gql`
    type FilterValue {
        id: ID!
        key: String!
        value: String!
    }
    
    type Filter {
        id: ID!
        filters: [FilterValue!]
    }
`;