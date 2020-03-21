import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const CREATE_DOCUMENT = gql`
    mutation createDocument (
        $name: String!,
        $fileDescriptorId: String!,
        $description: String!,
        $tags: String!,
        $elaborationDate: String!,
        $authorId: String!,
        $visibility: String!,
    ) {
        createDocument (
            name: $name, 
            authorId: $authorId,
            description: $description, 
            tags: $tags,
            elaborationDate: $elaborationDate,
            fileDescriptorId: $fileDescriptorId,    
            visibility: $visibility,
        ) {
            ...DocumentFragment
        }
    }
    ${DOCUMENT_FRAGMENT}
`;
