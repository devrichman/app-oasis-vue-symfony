import gql from 'graphql-tag'
import {ME_FRAGMENT} from "@/graphql/security/me-fragment";
export const UPDATE_ME = gql`
    mutation updateMe (
        $firstName: String!,
        $lastName: String!,
        $email: String!,
        $phone: String!,
        $civility: String,
        $address: String,
        $linkedin: String,
        $function: String,
        $seniorityDate: String,
        $previousFunction: String,
        $profilePictureId: String,
    ) {
        updateMe (
            firstName: $firstName, 
            lastName: $lastName, 
            email: $email, 
            phone: $phone, 
            civility: $civility,
            address: $address,
            linkedin: $linkedin,
            function: $function,
            seniorityDate: $seniorityDate,
            previousFunction: $previousFunction,
            profilePictureId: $profilePictureId,
        ) {
           ... on SerializableUser {
                ...MeFragment
            }
        }
    }
    ${ME_FRAGMENT}
`;
