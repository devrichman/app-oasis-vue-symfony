import gql from 'graphql-tag'

export const CREATE_EVENT_MODEL = gql`
    mutation createEventModel (
        $name: String!,
        $description: String!,
        $type: String!,
        $programModelId: String,
    ) {
        createEventModel (
            name: $name,
            description: $description,
            type: $type,
            programModelId: $programModelId,
        ) {
            id,
            name,
            description,
            type,
        }
    }
`;
