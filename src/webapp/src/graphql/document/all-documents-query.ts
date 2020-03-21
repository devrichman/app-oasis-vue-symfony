import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const ALL_DOCUMENTS = gql`
    query allDocuments ($search: String, $visibility: String, $sortColumn: String, $sortDirection: String, $offset: Int, $limit: Int) {
        allDocuments(search: $search, visibility: $visibility, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items(offset: $offset, limit: $limit) {
                ...DocumentFragment
            },
            count,
        }
    }
    ${DOCUMENT_FRAGMENT}
`;
