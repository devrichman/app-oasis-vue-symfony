import gql from 'graphql-tag'
import {USER_FRAGMENT} from './user-fragment';

export const USER_BY_ID = gql`
    query userById ($id: String!) {
        userById (id: $id) {
            programsByProgramsUsers {
                id,
                name,
                status,
                type,
                dateStart,
                dateEnd,
                coach {
                    id,
                    firstName,
                    lastName,
                },
                events {
                    count,
                }
            },
            ...UserFragment
        }
    }
    ${USER_FRAGMENT}
`;
