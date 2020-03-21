import gql from 'graphql-tag'
import {COMPANY_FRAGMENT} from './company-fragment';

export const ALL_COMPANIES = gql`
    query allCompanies ($search: String, $offset: Int, $limit: Int, $sortColumn: String, $sortDirection: String) {
        allCompanies(search: $search, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items(offset: $offset, limit: $limit) {
             ...CompanyFragment
            },
            count,
        }
    }
    ${COMPANY_FRAGMENT}
`;
