import gql from 'graphql-tag';

export const ALL_FILTERS = gql`
    query filters{
        filters @client {
            id
            filters {
                id
                key
                value
            }
        }
    }
`;