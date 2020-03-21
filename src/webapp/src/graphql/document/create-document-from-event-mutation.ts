import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const CREATE_DOCUMENT_FROM_EVENT = gql`
    mutation createDocumentFromEvent (
        $name: String!,
        $fileDescriptorId: String!,
        $description: String!,
        $tags: String!,
        $authorId: String!,
        $eventId: String!
    ) {
        createDocumentFromEvent (
            name: $name, 
            authorId: $authorId,
            description: $description, 
            tags: $tags,
            fileDescriptorId: $fileDescriptorId,    
            eventId: $eventId
        ) {
            ...DocumentFragment
        }
    }
    ${DOCUMENT_FRAGMENT}
`;
