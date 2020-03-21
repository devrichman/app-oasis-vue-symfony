import gql from 'graphql-tag'

export const DELETE_EVENT = gql`
    mutation deleteEvent ($eventId: String!) {
        deleteEvent (eventId: $eventId) {
            id
        }
    }
`;
