# EndPoint Cheat Sheet

[GET /1.0/](listAll.md)

## Profiles API

### Profiles

[GET /1.0/profiles](profiles/listAll.md)

[DELETE /1.0/profiles](profiles/deleteAll.md)

[GET /1.0/profiles/{userName}](profiles/getOne.md)

[PUT /1.0/profiles/{userName}](profiles/updateOne.md)

[DELETE /1.0/profiles/{userName}](profiles/deleteOne.md)

### Profile Attributes

[GET /1.0/profiles/{userName}/attributes](profiles/attributes/listAll.md)

[DELETE /1.0/profiles/{userName}/attributes](profiles/attributes/deleteAll.md)

[GET /1.0/profiles/{userName}/attributes/{attributeName}](profiles/attributes/getOne.md)

[POST /1.0/profiles/{userName}/attributes/{attributeName}](profiles/attributes/createNew.md)

[DELETE /1.0/profiles/{userName}/attributes/{attributeName}](profiles/attributes/deleteOne.md)

### Profile Warnings

[GET /1.0/profiles/{userName}/warnings](profiles/warnings/listAll.md)

[DELETE /1.0/profiles/{userName}/warnings](profiles/warnings/deleteAll.md)

[GET /1.0/profiles/{userName}/warnings/{warningSlug}](profiles/warnings/getOne.md)

[POST /1.0/profiles/{userName}/warnings/{warningSlug}](profiles/warnings/createNew.md)

[DELETE /1.0/profiles/{userName}/warnings/{warningSlug}](profiles/warnings/deleteOne.md)

### Profile Gates

[GET /1.0/profiles/{userName}/gates](profiles/gates/listAll.md)

[DELETE /1.0/profiles/{userName}/gates](profiles/gates/deleteAll.md)

[GET /1.0/profiles/{userName}/gates/{gateSlug}](profiles/gates/getOne.md)

[POST /1.0/profiles/{userName}/gates/{gateSlug}](profiles/gates/createNew.md)

[DELETE /1.0/profiles/{userName}/gates/{gateSlug}](profiles/gates/deleteOne.md)

### Profile Attribute References

[GET /1.0/profiles/{userName}/references](profiles/references/listAll.md)

[POST /1.0/profiles/{userName}/references](profiles/references/createNew.md)

[DELETE /1.0/profiles/{userName}/references](profiles/references/deleteAll.md)

[GET /1.0/profiles/{userName}/references/{attributeName}](profiles/references/getOne.md)

[DELETE /1.0/profiles/{userName}/references/{attributeName}](profiles/references/deleteOne.md)

### Profile Attribute Reviews

[GET /1.0/profiles/{userName}/reviews](profiles/reviews/listAll.md)

[DELETE /1.0/profiles/{userName}/reviews](profiles/reviews/deleteAll.md)

[GET /1.0/profiles/{userName}/reviews/{attributeName}](profiles/reviews/getOne.md)

[POST /1.0/profiles/{userName}/reviews/{attributeName}](profiles/reviews/createNew.md)

[DELETE /1.0/profiles/{userName}/reviews/{attributeName}](profiles/reviews/deleteOne.md)

[GET /1.0/profiles/{userName}/reviews/{attributeName}/:id](profiles/reviews/getOne.md)

[DELETE /1.0/profiles/{userName}/reviews/{attributeName}/:id](profiles/reviews/deleteOne.md)

### Profile Sources

[GET /1.0/profiles/{userName}/sources](profiles/sources/listAll.md)

[DELETE /1.0/profiles/{userName}/sources](profiles/sources/deleteAll.md)

[PUT /1.0/profiles/{userName}/sources/{sourceId}](profiles/sources/createNew.md)

[DELETE /1.0/profiles/{userName}/sources/{sourceId}](profiles/sources/deleteOne.md)

#### Normalised Data

[GET /1.0/profiles/{userName}/normalised](profiles/normalised/listAll.md)

[POST /1.0/profiles/{userName}/normalised](profiles/normalised/createNew.md)

[DELETE /1.0/profiles/{userName}/normalised](profiles/normalised/deleteAll.md)

[GET /1.0/profiles/{userName}/normalised/:itemName](profiles/normalised/getOne.md)

[DELETE /1.0/profiles/{userName}/normalised/:itemName](profiles/normalised/deleteOne.md)

#### Digested Data

[GET /1.0/profiles/{userName}/digested](profiles/sources/features/listAll.md)

[POST /1.0/profiles/{userName}/digested](profiles/sources/features/createNew.md)

[DELETE /1.0/profiles/{userName}/digested](profiles/sources/features/deleteAll.md)

[GET /1.0/profiles/{userName}/digested/:itemName](profiles/sources/features/getOne.md)

[DELETE /1.0/profiles/{userName}/digested/:itemName](profiles/sources/features/deleteOne.md)

### Profile Tags

[GET /1.0/profiles/{userName}/tags](profiles/tags/listAll.md)

[DELETE /1.0/profiles/{userName}/tags](profiles/tags/deleteAll.md)

[GET /1.0/profiles/{userName}/tags/{tagSlug}](profiles/tags/getOne.md)

[POST /1.0/profiles/{userName}/tags/{tagSlug}](profiles/tags/createNew.md)

[DELETE /1.0/profiles/{userName}/tags/{tagSlug}](profiles/tags/deleteOne.md)

### Profile Tasks

[GET /1.0/profiles/{userName}/processes](profiles/processes/listAll.md)

[GET /1.0/profiles/{userName}/processes/:processId](profiles/processes/getOne.md)

[GET /1.0/profiles/{userName}/processes/:processId/tasks](profiles/tasks/listAll.md)

[POST /1.0/profiles/{userName}/processes/:processId/tasks](profiles/tasks/createNew.md)

[GET /1.0/profiles/{userName}/processes/:processId/tasks/:taskId](profiles/tasks/getOne.md)

[PUT /1.0/profiles/{userName}/processes/:processId/tasks/:taskId](profiles/tasks/updateOne.md)

## SSO API

[GET /1.0/sso](sso/listAll.md)

[GET /1.0/sso/{providerName}](sso/getOne.md)

[POST /1.0/sso](sso/createNew.md)

## Access API

[GET /1.0/access](access/listAll.md)

### Role Access

[GET /1.0/access/roles](access/roles/listAll.md)

[POST /1.0/access/roles](access/roles/createNew.md)

[DELETE /1.0/access/roles](access/roles/deleteAll.md)

[GET /1.0/access/roles/{roleAccessId}](access/roles/getOne.md)

[PUT /1.0/access/roles/{roleAccessId}](access/roles/updateOne.md)

[DELETE /1.0/access/roles/{roleAccessId}](access/roles/deleteOne.md)

### Company Access

[GET /1.0/access/companies](access/companies/listAll.md)

[GET /1.0/access/companies/{companySlug}](access/companies/getOne.md)

[POST /1.0/access/companies/{companySlug}](access/companies/updateCompany.md)

[DELETE /1.0/access/companies/{companySlug}](access/companies/deleteOne.md)

#### Company Members

[GET /1.0/access/management/members](access/management/listAll.md)

[GET /1.0/access/management/members/:roleName](access/management/getOne.md)

[POST /1.0/access/management/members/:roleName](access/management/updateRole.md)

[DELETE /1.0/access/management/members/:roleName](access/management/deleteOne.md)

#### Company Users

[GET /1.0/access/companies/{companySlug}/users](access/companies/users/listAll.md)

[GET /1.0/access/companies/{companySlug}/users/{userName}](access/companies/users/getOne.md)

[POST /1.0/access/companies/{companySlug}/users/{userName}](access/companies/users/updateUser.md)

[DELETE /1.0/access/companies/{companySlug}/users/{userName}](access/companies/users/deleteOne.md)

## Companies API

### Companies

[GET /1.0/companies](companies/listAll.md)

[POST /1.0/companies/:companySlug](companies/createNew.md)

[DELETE /1.0/companie:companySlugs](companies/deleteAll.md)

[GET /1.0/companies/{companySlug}](companies/getOne.md)

[PUT /1.0/companies/{companySlug}](companies/updateOne.md)

[DELETE /1.0/companies/{companySlug}](companies/deleteOne.md)

### Company Permissions

[GET /1.0/companies/{companySlug}/permissions](companies/permissions/listAll.md)

[POST /1.0/companies/{companySlug}/permissions](companies/permissions/createNew.md)

[DELETE /1.0/companies/{companySlug}/permissions](companies/permissions/deleteAll.md)

[GET /1.0/companies/{companySlug}/permissions/{routeName}](companies/permissions/getOne.md)

[PUT /1.0/companies/{companySlug}/permissions/{routeName}](companies/permissions/updateOne.md)

[DELETE /1.0/companies/{companySlug}/permissions/{routeName}](companies/permissions/deleteOne.md)

### Company Credentials

[GET /1.0/management/credentials](companies/credentials/listAll.md)

[POST /1.0/management/credentials](companies/credentials/createNew.md)

[DELETE /1.0/management/credentials](companies/credentials/deleteAll.md)

[GET /1.0/management/credentials/{pubKey}](companies/credentials/getOne.md)

[POST /1.0/management/credentials/{pubKey}](companies/credentials/updateOne.md)

[DELETE /1.0/management/credentials/{pubKey}](companies/credentials/deleteOne.md)

### Credential Hooks

[GET /1.0/management/credentials/{pubKey}/hooks](management/hooks/listAll.md)

[POST /1.0/management/credentials/{pubKey}/hooks](management/hooks/createNew.md)

[DELETE /1.0/management/credentials/{pubKey}/hooks](management/hooks/deleteAll.md)

[GET /1.0/management/credentials/{pubKey}/hooks/{hookId}](management/hooks/getOne.md)

[POST /1.0/management/credentials/{pubKey}/hooks/{hookId}](management/hooks/updateOne.md)

[DELETE /1.0/management/credentials/{pubKey}/hooks/{hookId}](management/hooks/deleteOne.md)

### Company Settings

[GET /1.0/management/settings](management/settings/listAll.md)

[POST /1.0/management/settings](management/settings/createNew.md)

[DELETE /1.0/management/settings](management/settings/deleteAll.md)

[GET /1.0/management/settings/{settingId}](management/settings/getOne.md)

[PUT /1.0/management/settings/{settingId}](management/settings/updateOne.md)

[DELETE /1.0/management/settings/{settingId}](management/settings/deleteOne.md)

### Company Members

[GET /1.0/management/members](companies/members/listAll.md)

[POST /1.0/management/members](companies/members/createNew.md)

[DELETE /1.0/management/members](companies/members/deleteAll.md)

[GET /1.0/management/members/{memberId}](companies/members/getOne.md)

[POST /1.0/management/members/{memberId}](companies/members/updateOne.md)

[DELETE /1.0/management/members/{memberId}](companies/members/deleteOne.md)

## Metrics API

[GET /1.0/metrics](metrics/listAll.md)

[GET /1.0/metrics/:metricName](metrics/getOne.md)

## Services API

[GET /1.0/services](services/listAll.md)

[POST /1.0/services](services/createNew.md)

[DELETE /1.0/services](services/deleteAll.md)

[GET /1.0/services/{serviceId}](services/getOne.md)

[PUT /1.0/services/{serviceId}](services/updateOne.md)

[DELETE /1.0/services/{serviceId}](services/deleteOne.md)

##  Service Handlers API

[GET /1.0/service-handlers](service-handlers/listAll.md)

[POST /1.0/service-handlers](service-handlers/createNew.md)

[DELETE /1.0/service-handlers](service-handlers/deleteAll.md)

[GET /1.0/service-handlers/{serviceHandlerId}](service-handlers/getOne.md)

[PUT /1.0/service-handlers/{serviceHandlerId}](service-handlers/updateOne.md)

[DELETE /1.0/service-handlers/{serviceHandlerId}](service-handlers/deletOne.md)
