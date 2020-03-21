import gql from 'graphql-tag'
import {COMPANY_FRAGMENT} from './company-fragment';

export const UPDATE_COMPANY = gql`
    mutation updateCompany (
        $id: String!,
        $name: String!,
        $salesforceLink: String,      
    ) {
        updateCompany (
            id: $id,
            name: $name,
            salesforceLink: $salesforceLink
        ) {
            ...CompanyFragment
        }
    }
    ${COMPANY_FRAGMENT}
`;
