group "default" {
    targets = ["base"]
}

target "base" {
    dockerfile = "Dockerfile.base"
    tags = ["advisingapp-base:latest"]
}