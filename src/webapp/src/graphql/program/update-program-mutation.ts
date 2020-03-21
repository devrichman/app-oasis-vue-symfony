import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';

export const UPDATE_PROGRAM = gql`
    mutation updateProgram (
        $id: String!,
        $name: String!,
        $description: String!,
        $type: String!,
        $userIds: [String!]!,
        $coachId: String,
        $modelId: String,
    ) {
        updateProgram (
            id: $id,
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
