import gql from 'graphql-tag'

export const DOCUMENT_FRAGMENT = gql`
    fragment DocumentFragment on Document {
        id,
        name,
        tags,
        description,
        elaborationDate,
        fileDescriptor {
            id,
            name
        }
        visibility,
        createdAt,
        author{
            id,
            profilePicture {
                id
            },
            firstName,
            lastName,
        }
    }
`;
