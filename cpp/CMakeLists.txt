cmake_minimum_required(VERSION 3.0.0)
project(otel-cpp-example VERSION 0.1.0 LANGUAGES C CXX)

add_executable(otel-cpp-example main.cpp)

set(CPACK_PROJECT_NAME ${PROJECT_NAME})
set(CPACK_PROJECT_VERSION ${PROJECT_VERSION})
include(CPack)


include(FindProtobuf)
find_package(Protobuf REQUIRED)
include_directories(${PROTOBUF_INCLUDE_DIR})

set(opentelemetry-cpp_DIR "./include/lib/cmake/opentelemetry-cpp")
find_package(opentelemetry-cpp CONFIG REQUIRED)

target_include_directories(otel-cpp-example PRIVATE ${OPENTELEMETRY_CPP_INCLUDE_DIRS})
target_link_libraries(otel-cpp-example PRIVATE ${OPENTELEMETRY_CPP_LIBRARIES})
