import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';

export const CREATE_PROGRAM = gql`
    mutation createProgram (
        $name: String!,
        $description: String!,
        $type: String!,
        $userIds: [String!]!,
        $coachId: String,
        $modelId: String,
    ) {
        createProgram (
            name: $name, 
            description: $description, 
            type: $type,
            userIds: $userIds,
            coachId: $coachId,
            modelId: $modelId,
        ) {
            ...ProgramFragment
        }
    }
    ${PROGRAM_FRAGMENT}
`;
