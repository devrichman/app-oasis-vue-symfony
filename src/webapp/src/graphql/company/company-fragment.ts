import gql from 'graphql-tag'

export const COMPANY_FRAGMENT = gql`
    fragment CompanyFragment on Company {
            id,
            name,
            code,
            salesforceLink,
    }
`;
