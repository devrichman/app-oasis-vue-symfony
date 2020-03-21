import gql from 'graphql-tag'
import {USER_FRAGMENT} from './user-fragment';

export const IMPORT_USERS = gql`
    mutation importUsers ($file: Upload!) {
        importUsers (file: $file) {
            ...UserFragment
        }
    }
    ${USER_FRAGMENT}
`;
