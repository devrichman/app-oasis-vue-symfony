import gql from 'graphql-tag'

export const UPLOAD_PICTURE = gql`
    mutation uploadPicture ($file: Upload!) {
        uploadPicture (file: $file) {
            id,
            name,
            size,
        }
    }
`;
