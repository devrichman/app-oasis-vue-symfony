import gql from 'graphql-tag'

export const EVENT_MODEL_BY_ID = gql`
    query eventModelById ($eventModelId: String!) {
        eventModelById (eventModelId: $eventModelId) {
            id,
            name,
            description,
            type,
            programModel{
                id
            },
            createdAt,
            createdBy {
                id,
                firstName,
                lastName,
            },
            updatedAt,
            updatedBy {
                id,
                firstName,
                lastName,
            },
        }
    }
`;
