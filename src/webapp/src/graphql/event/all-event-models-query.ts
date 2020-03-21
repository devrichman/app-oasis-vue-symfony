import gql from 'graphql-tag'

export const ALL_EVENT_MODELS = gql`
    query allEventModels ($search: String, $sortColumn: String, $sortDirection: String, $offset: Int, $limit: Int) {
        allEventModels (search: $search, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items (offset: $offset, limit: $limit) {
                id,
                name,
                description,
                type,
                createdAt,
                updatedAt,
                events {
                    count,
                }
            },
            count,
        }
    }
`;
