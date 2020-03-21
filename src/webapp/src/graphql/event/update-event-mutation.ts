import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from './event-fragment';

export const UPDATE_EVENT = gql`
    mutation updateEvent (
        $id: String!,
        $name: String!,
        $description: String!,
        $type: String!,
        $userIds: [String!]!,
        $organizerId: String,
        $dateEvent: String,
        $modelId: String,
    ) {
        updateEvent (
            id: $id,
            name: $name, 
            description: $description, 
            type: $type,
            userIds: $userIds,
            organizerId: $organizerId,
            dateEvent: $dateEvent,
            modelId: $modelId,
        ) {
            ...EventFragment
        }
    }
    ${EVENT_FRAGMENT}
`;
