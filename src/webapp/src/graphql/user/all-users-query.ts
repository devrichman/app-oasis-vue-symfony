import gql from 'graphql-tag'
import {USER_FRAGMENT} from './user-fragment';

export const ALL_USERS = gql`
    query allUsers ($search: String, $companyName: String, $coachesOnly: Boolean, $companyId: String, $role: String, $sortColumn: String, $sortDirection: String, $offset: Int, $limit: Int) {
        allUsers(search: $search, companyName: $companyName, coachesOnly: $coachesOnly, companyId: $companyId, role: $role, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items(offset: $offset, limit: $limit) {
                ...UserFragment
            },
            count,
        }
    }
    ${USER_FRAGMENT}
`;
