import gql from 'graphql-tag'

export const DELETE_EVENT_MODEL = gql`
    mutation deleteEventModel ($eventModelId: String!) {
        deleteEventModel (eventModelId: $eventModelId) {
            id
        }
    }
`;
