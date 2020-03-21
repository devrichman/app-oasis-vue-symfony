import gql from 'graphql-tag'
import {USER_FRAGMENT} from './user-fragment';

export const UPDATE_USER = gql`
    mutation updateUser (
        $id: String!,
        $firstName: String!,
        $lastName: String!,
        $email: String!,
        $phone: String!,
        $typeId: String!,
        $roleIds: [String!]!,
        $civility: String,
        $address: String,
        $linkedin: String,
        $function: String,
        $seniorityDate: String,
        $previousFunction: String,
        $companyId: String,
        $coachId: String,
        $profilePictureId: String,
        $status: Boolean
    ) {
        updateUser (
            id: $id,
            firstName: $firstName, 
            lastName: $lastName, 
            email: $email, 
            phone: $phone, 
            typeId: $typeId, 
            roleIds: $roleIds, 
            civility: $civility,
            address: $address,
            linkedin: $linkedin,
            function: $function,
            seniorityDate: $seniorityDate,
            previousFunction: $previousFunction,
            companyId: $companyId,
            coachId: $coachId, 
            profilePictureId: $profilePictureId,
            status: $status,
        ) {
            ...UserFragment
        }
    }
    ${USER_FRAGMENT}
`;
