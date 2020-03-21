import gql from 'graphql-tag'

export const ALL_PROGRAMS = gql`
    query allPrograms ($search: String, $status: String, $sortColumn: String, $sortDirection: String, $offset: Int, $limit: Int) {
        allPrograms (search: $search, status: $status, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items (offset: $offset, limit: $limit) {
                id,
                name,
                description,
                status,
                type,
                dateStart,
                dateEnd,
                programModel {
                    id,
                    name,
                    description,
                },
                createdAt,
                updatedAt,
            },
            count,
        }
    }
`;
