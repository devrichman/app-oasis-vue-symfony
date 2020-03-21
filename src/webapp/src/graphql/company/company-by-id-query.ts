import gql from 'graphql-tag'
import {COMPANY_FRAGMENT} from './company-fragment';

export const COMPANY_BY_ID = gql`
    query companyById ($id: String!) {
        companyById (id: $id) {
            ...CompanyFragment
        }
    }
    ${COMPANY_FRAGMENT}
`;