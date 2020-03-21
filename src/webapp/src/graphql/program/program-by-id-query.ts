import gql from 'graphql-tag'
import {PROGRAM_FRAGMENT} from './program-fragment';

export const PROGRAM_BY_ID = gql`
    query programById ($programId: String!) {
        programById (programId: $programId) {
            ...ProgramFragment
        }
    }
    ${PROGRAM_FRAGMENT}
`;
