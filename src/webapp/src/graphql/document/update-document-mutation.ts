import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const UPDATE_DOCUMENT = gql`
    mutation updateDocument (
        $id: String!,
        $name: String!,
        $fileDescriptorId: String!,
        $description: String!,
        $tags: String!,
        $elaborationDate: DateTime!
        $authorId: String!,
        $visibility: String!   
    ) {
        updateDocument (
            id: $id,
            name: $name,
            elaborationDate: $elaborationDate,
            description: $description, 
            tags: $tags, 
            authorId: $authorId,
            fileDescriptorId: $fileDescriptorId,    
            visibility: $visibility  
        ) {
            ...DocumentFragment
        }
    }
    ${DOCUMENT_FRAGMENT}
`;
