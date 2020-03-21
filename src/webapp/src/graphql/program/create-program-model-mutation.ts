import gql from 'graphql-tag'
import {PROGRAM_MODEL_FRAGMENT} from "@/graphql/program/program-model-fragment";

export const CREATE_PROGRAM_MODEL = gql`
    mutation createProgramModel (
        $name: String!,
        $description: String!,
    ) {
        createProgramModel (
            name: $name, 
            description: $description, 
        ) {
            ...ProgramModelFragment
        }
    }
    ${PROGRAM_MODEL_FRAGMENT}
`;
