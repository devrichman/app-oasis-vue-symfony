import gql from 'graphql-tag'
import {DOCUMENT_FRAGMENT} from './document-fragment';

export const DOCUMENT_BY_ID = gql`
    query documentById ($id: String!) {
        documentById (id: $id) {
            ...DocumentFragment
        }
    }
    ${DOCUMENT_FRAGMENT}
`;
