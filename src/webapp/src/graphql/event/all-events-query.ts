import gql from 'graphql-tag'

export const ALL_EVENTS = gql`
    query allEvents ($search: String, $status: String, $sortColumn: String, $sortDirection: String, $offset: Int, $limit: Int) {
        allEvents (search: $search, status: $status, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items (offset: $offset, limit: $limit) {
                id,
                name,
                description,
                type,
                status,
                program {
                    id,
                    name,
                    description,
                    type,
                },
                dateEvent,
                createdAt,
                updatedAt,
            },
            count,
        }
    }
`;
