import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from './event-fragment';

export const CREATE_EVENT = gql`
    mutation createEvent (
        $name: String!,
        $description: String!,
        $type: String!,
        $userIds: [String!]!,
        $organizerId: String,
        $dateEvent: String,
        $modelId: String,
        $programId: String
    ) {
        createEvent (
            name: $name, 
            description: $description, 
            type: $type,
            userIds: $userIds,
            organizerId: $organizerId,
            dateEvent: $dateEvent,
            modelId: $modelId,
            programId: $programId,
        ) {
            ...EventFragment
        }
    }
    ${EVENT_FRAGMENT}
`;
