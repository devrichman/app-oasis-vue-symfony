import gql from 'graphql-tag'

export const UPDATE_EVENT_MODEL = gql`
    mutation updateEventModel (
        $id: String!
        $name: String!,
        $description: String!,
        $type: String!,
    ) {
        updateEventModel (
            id: $id,
            name: $name, 
            description: $description, 
            type: $type, 
        ) {
            id,
            name,
            description,
            type,
        }
    }
`;
