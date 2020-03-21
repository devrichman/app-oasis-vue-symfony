import gql from 'graphql-tag'
import {PROGRAM_MODEL_FRAGMENT} from "@/graphql/program/program-model-fragment";

export const UPDATE_PROGRAM_MODEL = gql`
    mutation updateProgramModel (
        $id: String!
        $name: String!,
        $description: String!,
    ) {
        updateProgramModel (
            id: $id,
            name: $name, 
            description: $description, 
        ) {
            ...ProgramModelFragment
        }
    }
    ${PROGRAM_MODEL_FRAGMENT}
`;
