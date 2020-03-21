import gql from 'graphql-tag'
import {PROGRAM_MODEL_FRAGMENT} from "@/graphql/program/program-model-fragment";

export const PROGRAM_MODEL_BY_ID = gql`
    query programModelById ($programModelId: String!) {
        programModelById (programModelId: $programModelId) {
            ...ProgramModelFragment
        }
    }
    ${PROGRAM_MODEL_FRAGMENT}
`;
