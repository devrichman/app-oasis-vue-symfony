import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from './event-fragment';

export const EVENT_BY_ID = gql`
    query eventById ($eventId: String!) {
        eventById (eventId: $eventId) {
            ...EventFragment,
            documents {
                id,
                name,
            }
        }
    }
    ${EVENT_FRAGMENT}
`;
