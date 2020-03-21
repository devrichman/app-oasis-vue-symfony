import gql from 'graphql-tag'

export const ALL_ROLES = gql`
    query allRoles ($search: String, $sortColumn: String, $sortDirection: String, $offset: Int, $limit: Int) {
        allRoles(search: $search, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items(offset: $offset, limit: $limit) {
                id,
                name,
                description,
                usersCount,
                rights {
                    code,
                },
            },
            count,
        }
    }
`;
