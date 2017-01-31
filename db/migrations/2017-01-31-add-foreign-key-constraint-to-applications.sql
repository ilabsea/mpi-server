ALTER TABLE application ADD FOREIGN KEY (scope_id) REFERENCES scope(id);
ALTER TABLE field_log ADD FOREIGN KEY (field_id) REFERENCES field(id);
ALTER TABLE field_value ADD FOREIGN KEY (field_id) REFERENCES field(id);

ALTER TABLE application_token ADD FOREIGN KEY (scope_id) REFERENCES scope(id);
ALTER TABLE application_token ADD FOREIGN KEY (application_id) REFERENCES application(id);
