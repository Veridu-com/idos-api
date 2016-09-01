# EndPoint Cheat Sheet

[GET /1.0/](listAll.md)

## Profiles API

### Profiles

[GET /1.0/profiles](profiles/listAll.md)

[DELETE /1.0/profiles](profiles/deleteAll.md)

[GET /1.0/profiles/:userName](profiles/getProfile.md)

[PUT /1.0/profiles/:userName](profiles/renameProfile.md)

[DELETE /1.0/profiles/:userName](profiles/deleteProfile.md)

### Profile Attributes

[GET /1.0/profiles/:userName/attributes](profiles/attributes/listAll.md)

[DELETE /1.0/profiles/:userName/attributes](profiles/attributes/deleteAll.md)

[GET /1.0/profiles/:userName/attributes/:attributeName](profiles/attributes/getAttribute.md)

[POST /1.0/profiles/:userName/attributes/:attributeName](profiles/attributes/createAttribute.md)

[DELETE /1.0/profiles/:userName/attributes/:attributeName](profiles/attributes/deleteAttribute.md)

[GET /1.0/profiles/:userName/attributes/:attributeName/:index](profiles/attributes/getIndexedAttribute.md)

[DELETE /1.0/profiles/:userName/attributes/:attributeName/:index](profiles/attributes/deleteIndexedAttribute.md)

### Profile Features

[GET /1.0/profiles/:userName/features](profiles/features/listAll.md)

[DELETE /1.0/profiles/:userName/features](profiles/features/deleteAll.md)

[POST /1.0/profiles/:userName/features](profiles/features/createNew.md)

[PUT /1.0/profiles/:userName/features/:featureSlug](profiles/features/updateOne.md)

[GET /1.0/profiles/:userName/features/:featureSlug](profiles/features/getOne.md)

[DELETE /1.0/profiles/:userName/features/:featureSlug](profiles/features/deleteOne.md)

### Profile Flags

[GET /1.0/profiles/:userName/flags](profiles/flags/listAll.md)

[DELETE /1.0/profiles/:userName/flags](profiles/flags/deleteAll.md)

[GET /1.0/profiles/:userName/flags/:flagName](profiles/flags/getFlag.md)

[POST /1.0/profiles/:userName/flags/:flagName](profiles/flags/createFlag.md)

[DELETE /1.0/profiles/:userName/flags/:flagName](profiles/flags/deleteFlag.md)

### Profile Gates

[GET /1.0/profiles/:userName/gates](profiles/gates/listAll.md)

[DELETE /1.0/profiles/:userName/gates](profiles/gates/deleteAll.md)

[GET /1.0/profiles/:userName/gates/:gateName](profiles/gates/getGate.md)

[POST /1.0/profiles/:userName/gates/:gateName](profiles/gates/createGate.md)

[DELETE /1.0/profiles/:userName/gates/:gateName](profiles/gates/deleteGate.md)

### Profile Attribute References

[GET /1.0/profiles/:userName/references](profiles/references/listAll.md)

[DELETE /1.0/profiles/:userName/references](profiles/references/deleteAll.md)

[GET /1.0/profiles/:userName/references/:attributeName](profiles/references/getReference.md)

[POST /1.0/profiles/:userName/references/:attributeName](profiles/references/createReference.md)

[DELETE /1.0/profiles/:userName/references/:attributeName](profiles/references/deleteReference.md)

### Profile Attribute Reviews

[GET /1.0/profiles/:userName/reviews](profiles/reviews/listAll.md)

[DELETE /1.0/profiles/:userName/reviews](profiles/reviews/deleteAll.md)

[GET /1.0/profiles/:userName/reviews/:attributeName](profiles/reviews/getReview.md)

[POST /1.0/profiles/:userName/reviews/:attributeName](profiles/reviews/createReview.md)

[DELETE /1.0/profiles/:userName/reviews/:attributeName](profiles/reviews/deleteReview.md)

[GET /1.0/profiles/:userName/reviews/:attributeName/:id](profiles/reviews/getIndexedReview.md)

[DELETE /1.0/profiles/:userName/reviews/:attributeName/:id](profiles/reviews/deleteIndexedReview.md)

### Profile Sources

[GET /1.0/profiles/:userName/sources](profiles/sources/listAll.md)

[DELETE /1.0/profiles/:userName/sources](profiles/sources/deleteAll.md)

[POST /1.0/profiles/:userName/sources/:sourceName](profiles/sources/createSource.md)

[DELETE /1.0/profiles/:userName/sources/:sourceName](profiles/sources/deleteSource.md)

#### E-mail OTP

[PUT /1.0/profiles/:userName/sources/email](profiles/sources/emailConfirmation.md)

#### SMS OTP

[PUT /1.0/profiles/:userName/sources/sms](profiles/sources/smsConfirmation.md)

#### Submitted Data

[PUT /1.0/profiles/:userName/sources/submitted](profiles/sources/updateSubmittedFields.md)

[PUT /1.0/profiles/:userName/sources/submitted/:fieldName](profiles/sources/updateSubmittedField.md)

#### Knowledge-based Authentication

[PUT /1.0/profiles/:userName/sources/spotafriend](profiles/sources/spotafriendConfirmation.md)

#### Source Mapped Data

[GET /1.0/profiles/:userName/sources/:sourceName/mapped](profiles/sources/mapped/listAll.md)

[DELETE /1.0/profiles/:userName/sources/:sourceName/mapped](profiles/sources/mapped/deleteAll.md)

[GET /1.0/profiles/:userName/sources/:sourceName/mapped/:itemName](profiles/sources/mapped/getItem.md)

[POST /1.0/profiles/:userName/sources/:sourceName/mapped/:itemName](profiles/sources/mapped/createItem.md)

[DELETE /1.0/profiles/:userName/sources/:sourceName/mapped/:itemName](profiles/sources/mapped/deleteItem.md)

#### Source Features

[GET /1.0/profiles/:userName/sources/:sourceName/features](profiles/sources/features/listAll.md)

[DELETE /1.0/profiles/:userName/sources/:sourceName/features](profiles/sources/features/deleteAll.md)

[GET /1.0/profiles/:userName/sources/:sourceName/features/:itemName](profiles/sources/features/getItem.md)

[POST /1.0/profiles/:userName/sources/:sourceName/features/:itemName](profiles/sources/features/createItem.md)

[DELETE /1.0/profiles/:userName/sources/:sourceName/features/:itemName](profiles/sources/features/deleteItem.md)

### Profile Tags

[GET /1.0/profiles/:userName/tags](profiles/tags/listAll.md)

[DELETE /1.0/profiles/:userName/tags](profiles/tags/deleteAll.md)

[GET /1.0/profiles/:userName/tags/:tagName](profiles/tags/getTag.md)

[POST /1.0/profiles/:userName/tags/:tagName](profiles/tags/createTag.md)

[DELETE /1.0/profiles/:userName/tags/:tagName](profiles/tags/deleteTag.md)

### Profile Tasks

[GET /1.0/profiles/:userName/processes](profiles/processes/listAll.md)

[GET /1.0/profiles/:userName/processes/:processId](profiles/processes/getOne.md)

[POST /1.0/profiles/:userName/processes/:processId](profiles/tasks/createNew.md)

[GET /1.0/profiles/:userName/processes/:processId/:taskId](profiles/tasks/getOne.md)

[PUT /1.0/profiles/:userName/processes/:processId/:taskId](profiles/tasks/updateOne.md)

## Tokens API

### Tokens

[GET /1.0/tokens](tokens/listAll.md)

[DELETE /1.0/tokens](tokens/deleteAll.md)

[GET /1.0/tokens/:userName](tokens/listUserTokens.md)

[POST /1.0/tokens/:userName](tokens/createUserToken.md)

### User Tokens

[GET /1.0/tokens/:userName/:token](tokens/getUserToken.md)

[POST /1.0/tokens/:userName/:token](tokens/extendUserToken.md)

[DELETE /1.0/tokens/:userName/:token](tokens/deleteUserToken.md)

## SSO API

[GET /1.0/sso](sso/listAll.md)

[GET /1.0/sso/:providerName](sso/getProvider.md)

[POST /1.0/sso/:providerName](sso/createSSO.md)

## Access API

[GET /1.0/access](access/listAll.md)

### Role Access

[GET /1.0/access/roles](access/roles/listAll.md)

[GET /1.0/access/roles/:roleName](access/roles/getGetOne.md)

[POST /1.0/access/roles/:roleName](access/roles/createNew.md)

[DELETE /1.0/access/roles](access/roles/deleteAll.md)

[DELETE /1.0/access/roles/:roleName](access/roles/deleteOne.md)

### Company Access

[GET /1.0/access/companies](access/companies/listAll.md)

[GET /1.0/access/companies/:companyId](access/companies/getCompany.md)

[POST /1.0/access/companies/:companyId](access/companies/updateCompany.md)

[DELETE /1.0/access/companies/:companyId](access/companies/deleteCompany.md)

#### Company Members

[GET /1.0/access/management/members](access/management/listAll.md)

[GET /1.0/access/management/members/:roleName](access/management/getRole.md)

[POST /1.0/access/management/members/:roleName](access/management/updateRole.md)

[DELETE /1.0/access/management/members/:roleName](access/management/deleteRole.md)

#### Company Users

[GET /1.0/access/companies/:companyId/users](access/companies/users/listAll.md)

[GET /1.0/access/companies/:companyId/users/:userName](access/companies/users/getUser.md)

[POST /1.0/access/companies/:companyId/users/:userName](access/companies/users/updateUser.md)

[DELETE /1.0/access/companies/:companyId/users/:userName](access/companies/users/deleteUser.md)

## Companies API

### Companies

[GET /1.0/companies](companies/listAll.md)

[POST /1.0/companies](companies/createNew.md)

[DELETE /1.0/companies](companies/deleteAll.md)

[GET /1.0/companies/:companyId](companies/getCompany.md)

[PUT /1.0/companies/:companyId](companies/updateCompany.md)

[DELETE /1.0/companies/:companyId](companies/deleteCompany.md)

### Company Permissions

[GET /1.0/commpanies/:companyId/permissions](companies/permissions/listAll.md)

[POST /1.0/companies/:companyId/permissions](companies/permissions/createNew.md)

[DELETE /1.0/companies/:companyId/permissions](companies/permissions/deleteAll.md)

[GET /1.0/companies/:companyId/permissions/:endpointName](companies/permissions/getOne.md)

[PUT /1.0/companies/:companyId/permissions/:endpointName](companies/permissions/updateOne.md)

[DELETE /1.0/companies/:companyId/permissions/:endpointName](companies/permissions/deleteOne.md)

### Company Credentials

[GET /1.0/management/credentials](companies/credentials/listAll.md)

[POST /1.0/management/credentials](companies/credentials/createNew.md)

[DELETE /1.0/management/credentials](companies/credentials/deleteAll.md)

[GET /1.0/management/credentials/:pubKey](companies/credentials/getCredential.md)

[POST /1.0/management/credentials/:pubKey](companies/credentials/updateCredential.md)

[DELETE /1.0/management/credentials/:pubKey](companies/credentials/deleteCredential.md)

### Credential Hooks

[GET /1.0/management/credentials/:pubKey/hooks](management/hooks/listAll.md)

[POST /1.0/management/credentials/:pubKey/hooks](management/hooks/createNew.md)

[DELETE /1.0/management/credentials/:pubKey/hooks](management/hooks/deleteAll.md)

[GET /1.0/management/credentials/:pubKey/hooks/:hookId](management/hooks/getHook.md)

[POST /1.0/management/credentials/:pubKey/hooks/:hookId](management/hooks/updateHook.md)

[DELETE /1.0/management/credentials/:pubKey/hooks/:hookId](management/hooks/deleteHook.md)

### Company Settings

[GET /1.0/management/settings](management/settings/listAll.md)

[DELETE /1.0/management/settings](management/settings/deleteAll.md)

[GET /1.0/management/settings/:category](management/settings/getCategory.md)

[DELETE /1.0/management/settings/:category](management/settings/deleteCategory.md)

[GET /1.0/management/settings/:category/:property](management/settings/getCategoryProperty.md)

[POST /1.0/management/settings/:category/:property](management/settings/setCategoryProperty.md)

[DELETE /1.0/management/settings/:category/:property](management/settings/unsetCategoryProperty.md)

### Company Members

[GET /1.0/companies/:companyId/members](companies/members/listAll.md)

[POST /1.0/companies/:companyId/members](companies/members/createNew.md)

[GET /1.0/companies/:companyId/members/:userName](companies/members/getMember.md)

[POST /1.0/companies/:companyId/members/:userName](companies/members/updateMember.md)

[DELETE /1.0/companies/:companyId/members/:userName](companies/members/deleteMember.md)

### Company Service Handlers

[GET /1.0/companies/:companyId/services](companies/services/listAll.md)

[GET /1.0/companies/:companyId/services/:serviceName](companies/services/listServiceHandlers.md)

[DELETE /1.0/companies/:companyId/services/:serviceName](companies/services/unsetServiceHandlers.md)

[GET /1.0/companies/:companyId/services/:serviceName/:handlerName](companies/services/getServiceHandler.md)

[POST /1.0/companies/:companyId/services/:serviceName/:handlerName](companies/services/setServiceHandler.md)

[DELETE /1.0/companies/:companyId/services/:serviceName/:handlerName](companies/services/unsetServiceHandler.md)

### Company Daemon Handlers

[GET /1.0/companies/:companyId/daemons](companies/daemons/listAll.md)

[GET /1.0/companies/:companyId/daemons/:daemonName](companies/daemons/listDaemonHandlers.md)

[DELETE /1.0/companies/:companyId/daemons/:daemonName](companies/daemons/unsetDaemonHandlers.md)

[GET /1.0/companies/:companyId/daemons/:daemonName/:handlerName](companies/daemons/getDaemonHandler.md)

[POST /1.0/companies/:companyId/daemons/:daemonName/:handlerName](companies/daemons/setDaemonHandler.md)

[DELETE /1.0/companies/:companyId/daemons/:daemonName/:handlerName](companies/daemons/unsetDaemonHandler.md)

## Metrics API

[GET /1.0/metrics](metrics/listAll.md)

[GET /1.0/metrics/:metricName](metrics/getMetric.md)

[POST /1.0/metrics/:metricName](metrics/setMetric.md)

[DELETE /1.0/metrics/:metricName](metrics/deleteMetric.md)

## Services API

[GET /1.0/services](services/listAll.md)

### Service Handlers
// Atualmente está assim:
[GET /1.0/services/:serviceName](services/listServiceHandlers.md)

[DELETE /1.0/services/:serviceName](services/deleteServiceHandlers.md)

[GET /1.0/services/:serviceName/:handlerName](services/getServiceHandler.md)

[POST /1.0/services/:serviceName/:handlerName](services/createServiceHandler.md)

[PUT /1.0/services/:serviceName/:handlerName](services/updateServiceHandler.md)

[DELETE /1.0/services/:serviceName/:handlerName](services/deleteServiceHandler.md)

// Proposta:
[GET /1.0/service-handlers](service-handlers/listAll.md)  (com sistema de filtragem)
// get one
[GET /1.0/service-handlers/:serviceSlug/:serviceHandlerSlug](service-handlers/getOne.md)
// delete all
[DELETE /1.0/service-handlers](service-handlers/deleteAll.md)
// create ( { serviceSlug: 'email-service' ... } )
[POST /1.0/service-handlers](service-handlers/createNew.md)
// update
[PUT /1.0/service-handlers/:serviceSlug/:serviceHandlerSlug](service-handlers/updateOne.md)
// delete one
[DELETE /1.0/service-handlers/:serviceSlug/:serviceHandlerSlug](service-handlers/deletOne.md)


## Daemons API

[GET /1.0/daemons](daemons/listAll.md)

### Daemon Handlers

[GET /1.0/daemons/:daemonName](daemons/listDaemonHandlers.md)

[DELETE /1.0/daemons/:daemonName](daemons/deleteDaemonHandlers.md)

[GET /1.0/daemons/:daemonName/:handlerName](daemons/getDaemonHandler.md)

[POST /1.0/daemons/:daemonName/:handlerName](daemons/createDaemonHandler.md)

[PUT /1.0/daemons/:daemonName/:handlerName](daemons/updateDaemonHandler.md)

[DELETE /1.0/daemons/:daemonName/:handlerName](daemons/deleteDaemonHandler.md)
