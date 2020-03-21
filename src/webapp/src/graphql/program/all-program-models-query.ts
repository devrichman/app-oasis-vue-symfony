import gql from 'graphql-tag'

export const ALL_PROGRAM_MODELS = gql`
    query allProgramModels ($search: String, $sortColumn: String, $sortDirection: String, $offset: Int, $limit: Int) {
        allProgramModels (search: $search, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items (offset: $offset, limit: $limit) {
                id,
                name,
                description,
                createdAt,
                updatedAt,
                programs {
                    count,
                },
                eventModels {
                    items {
                        id,
                        name,
                        description,
                        type,
                        createdAt,
                        updatedAt
                    },
                    count,
                },
            },
            count,
        }
    }
`;
